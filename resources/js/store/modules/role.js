import axios from 'axios'

export const state = {
    module_no: null,
    roles: []
};

export const getters = {
    module_no: state => state.module_no,
    roles: state => state.roles
};

export const actions = {
    async fetchRoles({commit}) {
        const response = await axios.get(
            'api/settings/roles'
        )
        commit('setRoles', response.data.allRoles)
    },

    async chooseModule({commit}) {
        commit('setModule', localStorage.getItem("module_no"))
    }
};

export const mutations = {
    setModule: (state, module_no) => (state.module_no = module_no),
    setRoles: (state, roles) => (state.roles = roles)
};