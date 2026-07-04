<script setup>
import { ref } from 'vue'

const props = defineProps({
  node:     { type: Object, required: true },
  activeId: { type: String, default: null },
  language: { type: String, default: 'Java' },
  depth:    { type: Number, default: 0 },
})
const emit = defineEmits(['click-node', 'new-node'])

const showCtx = ref(false)

const ICON = {
  project:   '📁',
  src:       '📂',
  package:   '📦',
  class:     '🟦',
  interface: '🔷',
  file:      '📄',
}

function icon(node) {
  if (node.type === 'project') return node.open ? '📂' : '📁'
  return ICON[node.type] || '📄'
}

function isFolder(node) {
  return ['project','src','package'].includes(node.type)
}

function newTypeForNode(node) {
  const l = props.language
  if (l === 'Java') {
    if (node.type === 'package') return ['Klasse','Interface']
    if (node.type === 'src') return ['Paket']
    if (node.type === 'project') return ['Paket']
  }
  return ['Datei']
}
</script>

<template>
  <div>
    <!-- Row -->
    <div
      class="tree-row"
      :class="{ active: activeId === node.id, folder: isFolder(node) }"
      :style="{ paddingLeft: (depth * 14 + 10) + 'px' }"
      @click="$emit('click-node', node)"
      @contextmenu.prevent="showCtx = !showCtx"
    >
      <span class="arrow" v-if="isFolder(node)">{{ node.open ? '▾' : '▸' }}</span>
      <span class="ti">{{ icon(node) }}</span>
      <span class="name">{{ node.name }}</span>
    </div>

    <!-- Kontextmenü -->
    <div v-if="showCtx" class="ctx-menu" @mouseleave="showCtx = false">
      <button
        v-for="t in newTypeForNode(node)" :key="t"
        @click="$emit('new-node', t, node.id); showCtx = false"
      >
        + Neue {{ t }}
      </button>
    </div>

    <!-- Kinder -->
    <template v-if="isFolder(node) && node.open">
      <TreeNode
        v-for="child in node.children" :key="child.id"
        :node="child"
        :active-id="activeId"
        :language="language"
        :depth="depth + 1"
        @click-node="$emit('click-node', $event)"
        @new-node="$emit('new-node', $event)"
      />
    </template>
  </div>
</template>

<style scoped>
.tree-row {
  display: flex; align-items: center; gap: 5px;
  height: 26px; cursor: pointer;
  font-size: .78rem; color: #a6adc8;
  user-select: none; position: relative;
  transition: background .1s;
}
.tree-row:hover { background: rgba(255,255,255,.07); }
.tree-row.active { background: rgba(124,58,237,.25); color: #cdd6f4; }
.arrow { font-size: .65rem; width: 10px; flex-shrink: 0; color: #6c7086; }
.ti { font-size: 12px; flex-shrink: 0; }
.name { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.ctx-menu {
  background: #2d2d3a; border: 1px solid rgba(255,255,255,.1);
  border-radius: 7px; padding: 4px; position: absolute;
  z-index: 50; min-width: 160px; margin-left: 30px;
  box-shadow: 0 6px 20px rgba(0,0,0,.4);
}
.ctx-menu button {
  display: block; width: 100%; background: none; border: none;
  color: #e2e8f0; font-size: .78rem; padding: 7px 10px;
  text-align: left; cursor: pointer; border-radius: 4px;
}
.ctx-menu button:hover { background: rgba(255,255,255,.1); }
</style>
