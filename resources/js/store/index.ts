import { RootState } from '@/js/store/types';
import api from "@/js/api";
import { createStore } from 'vuex'
import { IToast, SelectOptionsWithPlaceholder, ISelectOption } from '@/types';

// mutations enums
export enum RootMutations {
  SET_EQUIPMENT_TYPES = 'SET_USER_AUTHENTICATED',
  SET_SITEBAR_IS_OPEN = 'SET_SITEBAR_IS_OPEN',
  SET_TOASTS = 'SET_TOASTS'
}
// mutations enums
export enum RootActions {
  getEquipmentTypes = 'SET_USER_AUTHENTICATED',
}

export enum RootGetters {
  getEquipmentTypeOptions = 'getEquipmentTypeOptions'
}

const getLocalStorage = <T>(key: string, defaultValue: T): T => {
  try {
    return JSON.parse(localStorage[key]);
  } catch (error) {
    return defaultValue;
  }
}
const setLocalStorage = (key: string, value: unknown): void => {
  localStorage[key] = JSON.stringify(value);
}

const store = createStore<RootState>({
  state: {
    toasts: [],
    equipmentTypes: [],
    isSidebarOpen: getLocalStorage<boolean>('isSidebarOpen', true),
  },
  mutations: {
    [RootMutations.SET_EQUIPMENT_TYPES](state, payload) {
      state.equipmentTypes = payload;
    },
    [RootMutations.SET_TOASTS](state, payload: IToast) {
      state.toasts.push(payload);
    },
    [RootMutations.SET_SITEBAR_IS_OPEN](state, payload) {
      state.isSidebarOpen = payload;
      setLocalStorage('isSidebarOpen', payload);
    }
  },
  getters: {
    [RootGetters.getEquipmentTypeOptions](state): ISelectOption[] {
      return state.equipmentTypes.map(eq => ({
        value: eq.id.toString(),
        label: eq.name
      }));
    }
  },
  actions: {
    async [RootActions.getEquipmentTypes]({ state }) {
      try {
        const apiResponse = await api.services.equipmentType.getAll();
        state.equipmentTypes = apiResponse.data.data;
      } catch (e) {
        if (e instanceof Error) throw e;
      }
    }
  }
})


export default store;
