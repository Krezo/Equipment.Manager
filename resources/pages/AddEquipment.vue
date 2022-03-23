<template>
  <CContainer>
    <CRow>
      <CCol :col="12">
        <CCard>
          <CCardHeader><strong>Добавить оборудование</strong></CCardHeader>
          <CForm class="needs-validation" novalidation @submit.prevent>
            <CCardBody>
              <CAlert
                color="danger"
                v-if="Object.keys(this.form.apiError.errors).length"
              >
                <strong>{{ this.form.apiError.message }}</strong>
                <div
                  v-for="(errorMessage, errorFieldName) in this.form.apiError
                    .errors"
                  :key="errorFieldName"
                >
                  {{ errorMessage }}
                </div>
              </CAlert>
              <CAlert color="success" v-if="form.apiSucces">
                Оборудование успешно добавлено
              </CAlert>
              <div class="mb-3">
                <CFormSelect
                  :invalid="form.validators.equipmentTypeValidator.invalid"
                  :valid="form.validators.equipmentTypeValidator.valid"
                  v-model="form.equipmentType"
                  :options="equipmentTypesOptions"
                ></CFormSelect>
                <CFormFeedback
                  invalid
                  v-for="(error, index) in form.validators
                    .equipmentTypeValidator.errorBug"
                  :key="index"
                >
                  {{ error }}
                </CFormFeedback>
              </div>
              <div class="mb-3">
                <CFormLabel>Серийные номера</CFormLabel>
                <CFormTextarea
                  :invalid="
                    form.validators.equipmentSerialNumberValidator.invalid
                  "
                  :valid="form.validators.equipmentSerialNumberValidator.valid"
                  v-model="form.serialNumber"
                  rows="6"
                ></CFormTextarea>
                <CFormText>
                  При множественном вводе серийных номеров, используйте перенос
                  строки
                </CFormText>
                <CFormFeedback
                  invalid
                  v-for="(error, index) in form.validators
                    .equipmentSerialNumberValidator.errorBug"
                  :key="index"
                >
                  {{ error }}
                </CFormFeedback>
              </div>
              <div class="mb-3">
                <CFormLabel>Примечание</CFormLabel>
                <CFormTextarea v-model="form.remark"></CFormTextarea>
              </div>
              <CButton
                type="submit"
                color="primary"
                :disabled="this.form.btnLoading"
                @click="addEquipment"
                ><CSpinner
                  component="span"
                  size="sm"
                  aria-hidden="true"
                  class="me-1"
                  v-show="this.form.btnLoading"
                />Добавить</CButton
              >
            </CCardBody>
          </CForm>
        </CCard>
      </CCol>
    </CRow>
  </CContainer>
</template>

<script lang="ts">
import api, { ApiError } from "@/js/api/index";
import { RootActions } from "@/js/store";
import { defineComponent } from "vue";
import { SelectOptionsWithPlaceholder } from "@/types";
import ValidationForm from "@/js/validation/index";
import { AxiosError } from "axios";

export default defineComponent({
  data() {
    let equipmentType = "";
    let serialNumber = "";
    let remark = "";
    const equipmentTypeOptionsPlaceholder = "Выберите тип оборудования";
    const equipmentSerialNumberValidator: ValidationForm<string> =
      new ValidationForm([ValidationForm.RequiredRule()]);
    const equipmentTypeValidator: ValidationForm<string> = new ValidationForm([
      (equipmentType) =>
        /^\d+$/.test(equipmentType) || "Поле должно быть заполнено",
    ]);
    const apiResponseErrors = new ApiError();
    return {
      form: {
        btnLoading: false,
        apiError: apiResponseErrors,
        apiSucces: false,
        equipmentTypeOptionsPlaceholder,
        equipmentType,
        serialNumber,
        validators: { equipmentSerialNumberValidator, equipmentTypeValidator },
        remark,
      },
    };
  },
  methods: {
    validateEquipmentForm() {
      const validattorsResult = [
        this.form.validators.equipmentSerialNumberValidator.validate(
          this.form.serialNumber
        ),
        this.form.validators.equipmentTypeValidator.validate(
          this.form.equipmentType
        ),
      ];
      return validattorsResult.every((validator) => validator === true);
    },
    async addEquipment() {
      this.form.apiError.errors = {};
      this.form.btnLoading = true;
      this.form.apiSucces = false;
      if (!this.validateEquipmentForm()) {
        this.form.btnLoading = false;
        return;
      }
      const splitedSerialNumber = this.form.serialNumber.split(/\n/);
      try {
        await api.services.equipment.store({
          equipment_type_id: parseInt(this.form.equipmentType, 10),
          serial_number:
            splitedSerialNumber.length > 1
              ? splitedSerialNumber
              : this.form.serialNumber,
          remark: this.form.remark || null,
        });
        this.form.apiSucces = true;
      } catch (error) {
        if (api.isApiError(error)) {
          this.form.apiError = new ApiError(error as AxiosError);
        }
      }
      this.form.btnLoading = false;
    },
  },
  watch: {
    "form.equipemtnType"(newVal) {
      console.log(newVal);
    },
  },
  computed: {
    equipmentTypesOptions(): SelectOptionsWithPlaceholder {
      const equipmentTypesOptions = this.$store.state.equipmentTypes.map(
        (eq) => ({
          value: eq.id.toString(),
          label: `${eq.name} [${eq.mask}]`,
        })
      );
      return [
        this.form.equipmentTypeOptionsPlaceholder,
        ...equipmentTypesOptions,
      ];
    },
  },
  async mounted() {
    try {
      this.$store.dispatch(RootActions.getEquipmentTypes);
    } catch (e) {
      if (api.isApiError(e)) {
        console.dir("API ERROR");
      }
    }
  },
});
</script>

<style></style>
