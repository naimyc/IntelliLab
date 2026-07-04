<?php

namespace App\Services;

use RuntimeException;

/**
 * Zentrale KI-Logik für den ganzen Lern-Workflow.
 *
 * Wichtige Regeln (siehe Aufgabenstellung des Auftraggebers):
 *  - Die KI generiert AUSSCHLIESSLICH Aufgaben (Teilaufgaben) und Schritte.
 *    Sie erzeugt KEIN Java-Projekt/Package/Klassen/Interfaces/Methoden-Gerüst
 *    (starter_code) mehr — das baut der Nutzer selbst im Editor.
 *  - Text/Datei wird zuerst validiert: nur wenn es sich um eine echte,
 *    sinnvolle Programmieraufgabe handelt, wird weitergemacht. Enthält der
 *    Text mehrere Teile, von denen nur manche Programmieraufgaben sind,
 *    werden nur diese Teile übernommen.
 *  - Die Schritte für Aufgabe 2, 3, ... werden NICHT im Voraus erzeugt,
 *    sondern erst on-demand, nachdem die vorherige Aufgabe als gelöst gilt.
 */
class TaskGenerationService
{
    public function __construct(private GptService $gpt)
    {
    }

    /**
     * Schritt 1: Eingabetext (aus Datei oder direkt eingegeben) validieren
     * und in Programmier-Teilaufgaben aufteilen. Liefert nur die erste
     * Aufgabe bereits mit Schritten aus, alle weiteren Aufgaben nur als
     * Titel/Fragestellung (ihre Schritte werden später einzeln generiert).
     *
     * @throws RuntimeException wenn keine gültige Programmieraufgabe erkannt wird
     */
    public function analyseAndSplit(string $rawText, string $language, string $projectName): array
    {
        $rawText = trim($rawText);
        if (mb_strlen($rawText) < 10) {
            throw new RuntimeException('Der Text ist zu kurz, um eine Aufgabe zu erkennen.');
        }

        $system = <<<PROMPT
Du bist ein strenger Prüfer und Lernassistent für Programmieraufgaben ({$language}).

Deine Aufgabe:
1. Lies den folgenden Text (kann aus einer hochgeladenen Datei stammen, kann mehrere
   unabhängige Aufgaben/Absätze enthalten, kann auch NICHTS mit Programmierung zu tun haben).
2. Identifiziere NUR die Teile, die eine echte, sinnvolle Programmieraufgabe darstellen
   (z. B. "Implementiere eine Klasse...", "Schreibe eine Funktion...", "Erstelle ein Formular...").
3. Ignoriere alle Teile, die KEINE Programmieraufgabe sind (z. B. allgemeine Texte, Aufsätze,
   Rezepte, Smalltalk, Fragen ohne Programmierbezug) — nimm sie NICHT in die Ausgabe auf.
4. Wenn GAR KEIN Teil eine sinnvolle Programmieraufgabe ist, antworte NUR mit:
   {"valid": false, "reason": "kurze Begründung auf Deutsch"}
5. Wenn mindestens ein gültiger Programmierauftrag gefunden wurde, antworte NUR mit validem JSON:
{
  "valid": true,
  "title": "Kurzer Gesamttitel des Projekts",
  "topic": "Hauptthema (z. B. Vererbung, Schleifen, REST-API)",
  "explanation": "Kurze Erklärung des Konzepts, 2-3 Sätze, auf Deutsch",
  "tasks": [
    {
      "task_title": "Kurzer Titel der Teilaufgabe",
      "question": "Vollständige, unveränderte Fragestellung dieser Teilaufgabe auf Deutsch"
    }
  ]
}

WICHTIGE REGELN:
- Erzeuge NIEMALS Code, Klassennamen, Methoden-Signaturen oder ein Datei-/Projekt-Gerüst.
  Das erstellt der Lernende komplett selbst im Editor.
- "question" muss den ORIGINALTEXT der Teilaufgabe so genau wie möglich wiedergeben, nichts erfinden.
- Wenn nur EINE Programmieraufgabe erkannt wird, gib genau ein Element in "tasks" zurück.
- Antworte AUSSCHLIESSLICH mit validem JSON. Keine Markdown-Codeblöcke, keine Erklärung außerhalb des JSON.
PROMPT;

        $result = $this->gpt->askJson(
            prompt: "Projektname: {$projectName}\nSprache/Kategorie: {$language}\n\nText:\n\"\"\"\n{$rawText}\n\"\"\"",
            system: $system,
            options: ['temperature' => 0.15, 'max_tokens' => 2000],
        );

        if (empty($result['valid'])) {
            $reason = $result['reason'] ?? 'Im Text wurde keine sinnvolle Programmieraufgabe erkannt.';
            throw new RuntimeException($reason);
        }

        $tasks = $result['tasks'] ?? [];
        if (!is_array($tasks) || count($tasks) === 0) {
            throw new RuntimeException('Im Text wurde keine sinnvolle Programmieraufgabe erkannt.');
        }

        // Schritte nur für die erste Aufgabe generieren.
        $firstSteps = $this->generateStepsForTask($tasks[0]['question'] ?? $tasks[0]['task_title'], $language);

        $preparedTasks = [];
        foreach ($tasks as $i => $t) {
            $preparedTasks[] = [
                'task_id'          => $i + 1,
                'task_title'       => $t['task_title'] ?? ('Aufgabe ' . ($i + 1)),
                'question'         => $t['question'] ?? '',
                'steps'            => $i === 0 ? $firstSteps : [],
                'steps_generated'  => $i === 0,
                'solution_code'    => null,
                'done_steps'       => [],
                'feedback_history' => [],
                'completed'        => false,
            ];
        }

        return [
            'project_name'         => $projectName,
            'language'             => $language,
            'title'                => $result['title'] ?? $projectName,
            'topic'                => $result['topic'] ?? '',
            'explanation'          => $result['explanation'] ?? '',
            'tasks'                => $preparedTasks,
            'current_task_index'   => 0,
        ];
    }

    /**
     * Generiert die Schritte für EINE Teilaufgabe (Textbeschreibung -> Liste von Schritten).
     * Wird beim Erststart für Aufgabe 1 verwendet und danach on-demand für
     * Aufgabe 2, 3, ... aufgerufen, nachdem die vorherige Aufgabe gelöst wurde.
     */
    public function generateStepsForTask(string $question, string $language): array
    {
        $system = <<<PROMPT
Du bist ein Programmierlehrer für {$language}.
Du bekommst die Fragestellung EINER Teilaufgabe und zerlegst sie in klare, nummerierbare
Lernschritte, die der Lernende selbst im Editor umsetzen soll.

Antworte AUSSCHLIESSLICH mit validem JSON in genau diesem Schema:
{"steps": ["Schritt 1 ...", "Schritt 2 ...", "..."]}

REGELN:
- 3 bis 8 Schritte, je ein kurzer, konkreter Satz auf Deutsch.
- Die Schritte müssen exakt den Inhalt der Aufgabe widerspiegeln, nichts hinzuerfinden.
- KEIN Code, KEINE Klassennamen/Methoden-Signaturen vorgeben — nur die zu erledigenden
  gedanklichen/fachlichen Schritte (z. B. "Lege ein Attribut für ... an", "Implementiere
  die Methode, die ... berechnet").
PROMPT;

        $result = $this->gpt->askJson(
            prompt: "Fragestellung:\n\"\"\"\n{$question}\n\"\"\"",
            system: $system,
            options: ['temperature' => 0.2, 'max_tokens' => 800],
        );

        $steps = $result['steps'] ?? [];
        return is_array($steps) ? array_values($steps) : [];
    }

    /**
     * Prüft den eingereichten Code für die aktuelle Teilaufgabe und liefert
     * strukturiertes Feedback inkl. eines zuverlässigen "bestanden"-Flags,
     * damit das Frontend sicher weiß, ob die nächste Aufgabe freigeschaltet
     * werden darf, und inkl. der Analyse-Felder fürs Dashboard "Analyse Ergebnis".
     */
    public function pruefeCode(string $language, string $taskTitle, string $question, array $steps, string $code): array
    {
        $stepsText = implode("\n", array_map(fn ($s, $i) => ($i + 1) . ". $s", $steps, array_keys($steps)));

        $system = <<<PROMPT
Du bist ein {$language}-Lehrer und prüfst die Abgabe eines Lernenden.

Antworte AUSSCHLIESSLICH mit validem JSON in genau diesem Schema:
{
  "bestanden": true/false,
  "feedback": "Strukturiertes Feedback auf Deutsch, max. 250 Wörter: 1) Wurden alle Schritte umgesetzt? 2) Gibt es Fehler? 3) Was war gut? 4) Was verbessern?",
  "gelernt": "1-2 Sätze: was der Lernende in dieser Sprache/diesem Thema gerade gelernt/geübt hat",
  "verbessern": "1-2 Sätze: konkreter Punkt, an dem sich der Lernende noch verbessern sollte",
  "noch_lernen": "1-2 Sätze: was als nächstes sinnvoll wäre zu lernen"
}

"bestanden" ist NUR dann true, wenn der Code die Aufgabe und alle Schritte im Wesentlichen
korrekt umsetzt (kleinere Stilfragen sind kein Ausschlussgrund, fehlende Kernfunktionalität
oder grobe Fehler schon).
PROMPT;

        $result = $this->gpt->askJson(
            prompt: "Aufgabe: {$taskTitle}\nFrage:\n{$question}\n\nSchritte:\n{$stepsText}\n\nCode:\n```\n{$code}\n```",
            system: $system,
            options: ['temperature' => 0.25, 'max_tokens' => 900],
        );

        return [
            'bestanden'   => (bool) ($result['bestanden'] ?? false),
            'feedback'    => $result['feedback'] ?? 'Kein Feedback erhalten.',
            'gelernt'     => $result['gelernt'] ?? '',
            'verbessern'  => $result['verbessern'] ?? '',
            'noch_lernen' => $result['noch_lernen'] ?? '',
        ];
    }

    /**
     * Generiert eine vollständige Musterlösung für EINE Teilaufgabe — aber nur
     * on-demand (Klick auf "Lösung" im Editor), niemals automatisch beim
     * Projektstart. So bleibt die Regel gewahrt, dass die KI beim Anlegen
     * eines neuen Projekts ausschließlich Aufgaben/Schritte erzeugt.
     */
    public function generateSolution(string $language, string $taskTitle, string $question, array $steps): string
    {
        $stepsText = implode("\n", array_map(fn ($s, $i) => ($i + 1) . ". $s", $steps, array_keys($steps)));

        $system = <<<PROMPT
Du bist ein {$language}-Experte. Schreibe eine vollständige, korrekte Musterlösung
für die folgende Teilaufgabe.

REGELN:
- Antworte AUSSCHLIESSLICH mit Code, keine Markdown-Codeblöcke (keine ```), keine Erklärung davor oder danach.
- Der Code muss eigenständig lauffähig/vollständig sein und alle genannten Schritte umsetzen.
PROMPT;

        return $this->gpt->ask(
            prompt: "Aufgabe: {$taskTitle}\nFrage:\n{$question}\n\nSchritte:\n{$stepsText}",
            system: $system,
            options: ['temperature' => 0.2, 'max_tokens' => 1200],
        );
    }
}