import { EquipmentType } from "@/js/api/types";
import { IToast } from "@/types";

export interface RootState {
  equipmentTypes: EquipmentType[],
  toasts: IToast[],
  isSidebarOpen: boolean
}

