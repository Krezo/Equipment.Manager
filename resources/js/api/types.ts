export interface KeyValue<T> {
  [key: string]: T
}

export type ApiResourceWrapper<T> = { data: T }
export type ApiResourcePaginationWrapper<T> = ApiResourceWrapper<T> & IPaginatorData

export type IPaginatorData = {
  links: IPaginatorLinks,
  meta: IPaginatorMeta
}

export interface IPaginatorRequestParams {
  page?: number;
  per_page?: number;
}


export interface IMetaLink {
  url: string;
  label: string;
  active: boolean;
}

export interface IPaginatorMeta {
  current_page: number;
  from: number;
  last_page: number;
  links: IMetaLink[];
  path: string;
  per_page: string;
  to: number;
  total: number;
}

export interface IPaginatorLinks {
  first: string;
  last: string | null;
  prev: string | null;
  next: string | null;
}

export interface Equipment {
  id: number,
  equipment_type_id: number,
  serial_number: string,
  remark: string | null,
}

export interface StoreEquipment {
  id: number,
  equipment_type_id: number,
  serial_number: string | string[],
  remark: string | null,
}

export interface EquipmentType {
  id: number,
  name: string,
  mask: string
}

export interface IApiError {
  message: string | null,
  errors?: { [key: string]: string[] } | null
}

export type EquipmentData = ApiResourceWrapper<Equipment>
export type EquipmentTypeData = ApiResourceWrapper<EquipmentType>