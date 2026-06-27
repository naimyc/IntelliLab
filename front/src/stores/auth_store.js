// import { defineStore } from 'pinia';

// export const useAuthStore = defineStore('auth', {
//    state: () => ({
//        token: localStorage.getItem('token') || null,
//        user: null,
//        loading: false,
//    }),
//    getters: {
//        isAuthenticated: (state) => !!state.token,
//    },
//    actions: {
//        login(userInfo) {
//            this.token = userInfo.token
//            this.user = userInfo.user
//            this.loading = false
//            localStorage.setItem('token', userInfo.token)
//        },
//        logout() {
//            this.token = null
//            this.user = null
//            this.loading = false
//            localStorage.removeItem('token')
//        }
//    }
// })