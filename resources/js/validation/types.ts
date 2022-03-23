export interface Rule<T> {
  (validateData: T): true | string;
}

