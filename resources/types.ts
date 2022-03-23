export interface ISelectOption {
  value: string,
  label: string
}

export interface IToast {
  title?: string,
  message: string,
  color: 'primary' | 'secondary' | 'success' | 'danger' | 'warning' | 'info' | 'dark' | 'light' | string,
  autohide: boolean,
  delay: number
}

export class Toast implements IToast {
  public title?;
  public message;
  public color;
  public autohide;
  public delay;
  constructor(data: {
    message: string,
    title?: string,
    color?: "primary" | 'secondary' | 'success' | 'danger' | 'warning' | 'info' | 'dark' | 'light',
    autohide?: boolean,
    delay?: number
  }) {
    this.title = data.title || "";
    this.message = data.message;
    this.autohide = data.autohide || true;
    this.color = data.color || "primary";
    this.delay = data.delay || 10000;
  }
}

export type SelectValue = string | string[];
export type SelectOptionsWithPlaceholder = (ISelectOption | string)[];