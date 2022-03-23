<template>
  <nav v-if="paginatorData?.meta">
    <ul class="pagination">
      <li
        v-for="(link, index) in paginatorData.meta.links"
        class="page-item"
        :class="{ disabled: !link.url, active: link.active }"
        :key="link.label"
      >
        <a
          @click.prevent="changePage(link, index)"
          role="button"
          class="page-link"
          :href="link.url"
          :class="{ disabled: !link.url }"
          v-html="link.label"
        >
        </a>
      </li>
    </ul>
  </nav>
</template>

<script lang="ts">
import { defineComponent, PropType } from "vue";
import { IMetaLink, IPaginatorData } from "@/js/api/types";

export default defineComponent({
  data() {
    return {};
  },
  methods: {
    changePage(link: IMetaLink, linkIndex: number) {
      if (linkIndex == 0 && link.url) {
        this.$emit("change", this.paginatorData.meta.current_page - 1);
      }
      if (linkIndex === this.paginatorData.meta.links.length - 1 && link.url) {
        this.$emit("change", this.paginatorData.meta.current_page + 1);
      }
      const parsedPageNumber = parseInt(link.label, 10);
      if (
        parsedPageNumber &&
        parsedPageNumber !== this.paginatorData.meta.current_page
      )
        this.$emit("change", parsedPageNumber);
    },
  },
  emits: ["change"],
  props: {
    paginatorData: {
      type: Object as PropType<IPaginatorData>,
      required: true,
    },
  },
});
</script>


<style>
</style>