import { ApiResourceWrapper, Equipment, EquipmentType, ModelEquipment, IApiError, KeyValue, IPaginatorRequestParams, IPaginatorData, ApiResourcePaginationWrapper } from './types';
import axios, { AxiosError, AxiosInstance, AxiosResponse, } from "axios";
const axiosInstance = axios.create({
  baseURL: "/api",
  withCredentials: true
});

class RestService<ResponseData, Model>  {
  constructor(public axiosInstance: AxiosInstance, public endpoint: string) { }
  async getAll(params?: IPaginatorRequestParams & KeyValue<unknown>): Promise<AxiosResponse<ApiResourcePaginationWrapper<ResponseData[]>>> {
    return await this.axiosInstance.get(this.endpoint, {
      params
    })
  }
  async store(data: Model): Promise<AxiosResponse<void>> {
    return await this.axiosInstance.post(this.endpoint, data);
  }
  async show(id: number): Promise<ApiResourceWrapper<ResponseData>> {
    return await this.axiosInstance.get(this.endpoint, {
      params: { id }
    })
  }
  async update(id: number, data: Omit<Model, 'id'>): Promise<AxiosResponse<ApiResourceWrapper<ResponseData>>> {
    return await this.axiosInstance.patch(`${this.endpoint}/${id}`, data);
  }
  async delete(id: number): Promise<void> {
    return await this.axiosInstance.delete(`${this.endpoint}/${id}`);
  }
}

class EquipmentTypeService extends RestService<EquipmentType, EquipmentType> {
  constructor(public axiosInstance: AxiosInstance) {
    super(axiosInstance, 'equipment_type');
  }
}


interface EquipmentServiceSearchData extends IPaginatorRequestParams {
  serial_number: string | null
}

class EquipmentService extends RestService<Equipment, ModelEquipment> {
  constructor(public axiosInstance: AxiosInstance) {
    super(axiosInstance, 'equipment');
  }
  async store(data: Omit<ModelEquipment, 'id'>): Promise<AxiosResponse<void>> {
    return await this.axiosInstance.post(this.endpoint, data);
  }
  async search(params: EquipmentServiceSearchData): Promise<AxiosResponse<ApiResourcePaginationWrapper<Equipment[]>>> {
    return await this.axiosInstance.get(`${this.endpoint}`, {
      params
    });
  }
}

export class Api {
  constructor(private readonly axiosInstance: AxiosInstance) { }
  public readonly services = {
    equipmentType: new EquipmentTypeService(this.axiosInstance),
    equipment: new EquipmentService(this.axiosInstance),
  }
  public isAxiosError(e: unknown): boolean {
    return axios.isAxiosError(e);
  }
  public isApiError(e: unknown): boolean {
    if (!this.isAxiosError(e)) return false;
    const error = e as AxiosError;
    return !!error.response?.data?.message
      && !!error.response?.data?.errors;
  }
}

export class ApiError implements IApiError {
  public errors: { [key: string]: string[] } = {};
  public message: string;
  constructor(axiosError?: AxiosError) {
    this.errors = axiosError?.response?.data?.errors || {};
    this.message = axiosError?.response?.data?.message || {};
  }
  public errorToString(): string {
    let errorString = "";
    for (const error in this.errors) {
      errorString += this.errors[error] + '\n';
    }
    return errorString;
  }
}

const api = new Api(axiosInstance);

export default api;