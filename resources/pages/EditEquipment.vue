<template>
  <CContainer>
    <div class="bg-white border rounded overflow-hidden position-relative">
      <CTable class="equipment_table">
        <CTableHead color="light">
          <CTableRow>
            <CTableHeaderCell scope="col">Код оборудования</CTableHeaderCell>
            <CTableHeaderCell scope="col">Тип оборудования</CTableHeaderCell>
            <CTableHeaderCell scope="col">Серийный номер</CTableHeaderCell>
            <CTableHeaderCell scope="col">Примечание</CTableHeaderCell>
            <CTableHeaderCell scope="col">Действие</CTableHeaderCell>
          </CTableRow>
        </CTableHead>
        <CTableBody>
          <CTableRow>
            <CTableDataCell scope="row"></CTableDataCell>
            <CTableDataCell></CTableDataCell>
            <CTableDataCell
              ><CFormInput
                type="text"
                v-model.trim="searchSerialNumber"
                placeholder="Поиск по сер.номеру"
            /></CTableDataCell>
            <CTableDataCell></CTableDataCell>
            <CTableDataCell></CTableDataCell>
          </CTableRow>
          <template v-if="!isChangePage">
            <CTableRow
              v-for="(equipment, index) in this.equipments"
              :key="index"
            >
              <CTableDataCell>{{ equipment.id }}</CTableDataCell>
              <CTableDataCell>
                <CFormSelect
                  v-if="this.isEditMode && this.isEditEquipmentIndex === index"
                  v-model="equipment.equipment_type_id"
                  :options="equipmentOptions"
                >
                </CFormSelect>
                <span v-else>
                  {{ getEquipmentTypeName(equipment.equipment_type_id) }}</span
                >
              </CTableDataCell>
              <CTableDataCell>
                <template
                  v-if="this.isEditMode && this.isEditEquipmentIndex === index"
                >
                  <CFormInput
                    type="text"
                    v-model="equipment.serial_number"
                    placeholder="Введите серийный номер"
                  />
                  <CFormFeedback valid> Looks good! </CFormFeedback>
                </template>
                <span v-else>{{ equipment.serial_number }} </span>
              </CTableDataCell>
              <CTableDataCell>
                <CFormTextarea
                  v-if="this.isEditMode && this.isEditEquipmentIndex === index"
                  type="text"
                  v-model="equipment.remark"
                  placeholder="Введите примечание"
                />
                <span v-else> {{ equipment.remark }}</span>
              </CTableDataCell>
              <CTableDataCell>
                <CButton
                  class="me-2"
                  color="primary"
                  size="sm"
                  @click="editEquipment(equipment.id, index)"
                  ><CIcon icon="cil-color-border"
                /></CButton>
                <CButton
                  v-if="
                    !(this.isEditMode && this.isEditEquipmentIndex === index)
                  "
                  color="danger"
                  size="sm"
                  @click="deleteEquipment(equipment.id, index)"
                  ><CIcon icon="cilXCircle"
                /></CButton>
                <CButton
                  v-if="this.isEditMode && this.isEditEquipmentIndex === index"
                  color="success"
                  size="sm"
                  @click="saveEquipment(index)"
                  ><CIcon icon="cilCheckAlt"
                /></CButton>
              </CTableDataCell>
            </CTableRow>
          </template>
          <template v-else>
            <div class="t-body-loader-wrapper"></div>
            <div class="loder-spiner-wrapper">
              <CSpinner color="primary" />
            </div>
          </template>
        </CTableBody>
      </CTable>
    </div>
    <div v-if="!tableLoader" class="d-flex justify-content-center paginator">
      <VPaginator
        @change="changePage"
        v-if="paginatorData"
        :paginator-data="paginatorData"
      >
      </VPaginator>
    </div>
  </CContainer>
</template>

<script lang="ts">
import { debounce } from "lodash";

import { defineComponent } from "vue";
import { Equipment, IPaginatorData } from "@/js/api/types";
import api, { ApiError } from "@/js/api";
import VPaginator from "@/components/UI/VPaginator.vue";
import { RootActions, RootMutations, RootGetters } from "@/js/store";
import { Toast } from "@/types";
import { AxiosError } from "axios";

export default defineComponent({
  data() {
    // Данные для пагинатора
    let paginatorData: IPaginatorData | unknown = null;
    // Сохраненные данные перед редактированием
    let storeEditEquipment: Equipment | unknown = null;
    const equipments: Equipment[] = [];
    return {
      paginatorData,
      tableLoader: false,
      equipments,
      isEditMode: false,
      isEditEquipmentIndex: 0,
      perPage: 10,
      searchSerialNumber: "",
      isChangePage: false,
      storeEditEquipment,
    };
  },
  computed: {
    equipmentOptions() {
      return this.$store.getters[RootGetters.getEquipmentTypeOptions];
    },
  },
  components: { VPaginator },
  methods: {
    getEquipmentTypeName(equipment_type_id: number | string) {
      if (this.$store.state.equipmentTypes.length === 0) return;
      const eq = this.$store.state.equipmentTypes.find(
        (eqt) => eqt.id.toString() === equipment_type_id.toString()
      );
      return eq.name;
    },
    async loadEquipment(page = 1) {
      try {
        if (this.isChangePage) return;
        this.isChangePage = true;
        const apiResponse = (
          await api.services.equipment.getAll({
            page,
            per_page: this.perPage,
          })
        ).data;
        this.equipments = apiResponse.data;
        (this.paginatorData as IPaginatorData) = {
          links: apiResponse.links,
          meta: apiResponse.meta,
        };
        this.isChangePage = false;
      } catch (e) {
        this.$store.commit(
          RootMutations.SET_TOASTS,
          new Toast({
            message: "Ошибка при обработке запроса",
            color: "danger",
          })
        );
      }
    },
    async changePage(page: number) {
      this.loadEquipment(page);
    },
    async searchEquipemnt(serualNumber: string) {
      this.isEditMode = false;
      this.isChangePage = true;
      const apiResponse = (
        await api.services.equipment.search({
          serial_number: serualNumber || null,
          per_page: this.perPage,
        })
      ).data;
      this.equipments = apiResponse.data;
      (this.paginatorData as IPaginatorData) = {
        links: apiResponse.links,
        meta: apiResponse.meta,
      };
      this.isChangePage = false;
    },
    debounceSerachEquipment: debounce(async function () {
      await this.searchEquipemnt(this.searchSerialNumber);
    }, 1000),
    async deleteEquipment(equipmentId: number, index: number) {
      try {
        if (!confirm("Вы дейтсвительно хотите удалить объект?")) return;
        await api.services.equipment.delete(equipmentId);
      } catch (error) {
        this.$store.commit(
          RootMutations.SET_TOASTS,
          new Toast({
            message: "Произошла ошибка при удалении оборудования",
            color: "danger",
          })
        );
        return;
      }
      this.equipments.splice(index, 1);
      this.$store.commit(
        RootMutations.SET_TOASTS,
        new Toast({
          message: "Объект успешно удален",
          color: "success",
        })
      );
    },
    editEquipment(equipmentId: number, index: number) {
      if (this.storeEditEquipment) {
        this.equipments[this.isEditEquipmentIndex] = this
          .storeEditEquipment as Equipment;
      }
      this.storeEditEquipment = Object.assign({}, this.equipments[index]);
      if (this.isEditEquipmentIndex !== index) {
        this.isEditMode = true;
      } else this.isEditMode = !this.isEditMode;
      this.isEditEquipmentIndex = index;
    },
    async saveEquipment(saveEquipmentIndex: number) {
      try {
        await api.services.equipment.update(
          this.equipments[saveEquipmentIndex].id,
          {
            equipment_type_id:
              this.equipments[saveEquipmentIndex].equipment_type_id,
            serial_number: this.equipments[saveEquipmentIndex].serial_number,
            remark: this.equipments[saveEquipmentIndex].remark || null,
          }
        );
        this.isEditMode = false;
        this.storeEditEquipment = null;
        this.$store.commit(
          RootMutations.SET_TOASTS,
          new Toast({
            message: "Объект успешно обновлен",
            color: "success",
          })
        );
      } catch (e) {
        if (api.isApiError(e as AxiosError)) {
          console.log(e);
          const error = new ApiError(e as AxiosError);
          this.$store.commit(
            RootMutations.SET_TOASTS,
            new Toast({
              title: error.message,
              message: error.errorToString(),
              color: "danger",
            })
          );
        }
      }
    },
  },
  watch: {
    searchSerialNumber() {
      this.debounceSerachEquipment();
    },
  },
  async mounted() {
    this.loadEquipment();
    this.$store.dispatch(RootActions.getEquipmentTypes);
  },
});
</script>


<style lang="scss" scoped>
@use "sass:math";

$t-body-wrapper-height: 200px;
.equipment_table {
  & td {
    vertical-align: middle;
    height: 55px;
  }
}
.t-body-loader-wrapper {
  height: $t-body-wrapper-height;
}
.loder-spiner-wrapper {
  position: absolute;
  left: 50%;
  bottom: math.div($t-body-wrapper-height, 2);
  transform: translate(50%, 50%);
}
.paginator {
  padding-top: 20px;
}
</style>