import { RootState } from './js/store/types';
import { Store } from 'vuex'

declare module '@vue/runtime-core' {
  // provide typings for `this.$store`
  interface ComponentCustomProperties {
    $store: Store<RootState>
  }
}