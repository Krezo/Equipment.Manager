<template lang="pug">
div
  CSidebar(position="fixed", :visible="sidebarVisible")
    CSidebarBrand Меню
    CSidebarNav
      CNavItem
        li.nav-item
          router-link.nav-link(:to="{ name: 'home' }")
            strong Главная
      li.nav-title Оборудование
      li.nav-item
        router-link.nav-link(:to="{ name: 'add-equipment' }")
          CIcon(customClassName="nav-icon", icon="cilPlus")
          | Добавить
      li.nav-item
        router-link.nav-link(:to="{ name: 'edit-equipment' }")
          CIcon(customClassName="nav-icon", icon="cilColorBorder")
          | Редактировать
  .wrapper.d-flex.flex-column.min-vh-100
    CHeader
      CContainer(fluid)
        CHeaderToggler(@click="sidebarVisible = !sidebarVisible")
          CIcon(icon="cil-menu")
            router-link.header-brand(to="/") Header
    .page-layout
      router-view
  .toaster-wrapper 
    VToaster
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { RootMutations } from "./store";
import VToaster from "@/components/VToaster.vue";

export default defineComponent({
  computed: {
    sidebarVisible: {
      get(): boolean {
        return this.$store.state.isSidebarOpen;
      },
      set(value) {
        this.$store.commit(RootMutations.SET_SITEBAR_IS_OPEN, value);
      },
    },
  },
  components: {
    VToaster,
  },
});
</script>

<style lang="scss" scoped>
.wrapper {
  position: relative;
  padding-left: var(--cui-sidebar-occupy-start, 0);
  background-color: var(--cui-light, #ebedef) !important;
}
.toaster-wrapper {
  position: fixed;
  right: 0;
  bottom: 0;
}
.page-layout {
  padding: 20px;
}
</style>
