<template>
  <CToaster>
    <CToast
      v-for="(toast, index) in toasts"
      :key="index"
      :autohide="toast.autohide"
      :color="toast.color"
      :delay="toast.delay"
    >
      <CToastHeader>
        <span class="me-auto fw-bold" v-if="toast.title">{{
          toast.title
        }}</span>
      </CToastHeader>
      <CToastBody>{{ toast.message }}</CToastBody>
    </CToast>
  </CToaster>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  computed: {
    toasts() {
      return this.$store.state.toasts;
    },
  },
});
</script>


<style>
</style>