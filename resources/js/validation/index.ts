import { Rule } from "./types";

export default class FormValidation<T> {
  public errorBug: string[] = [];
  public validated: boolean | null = null;
  public invalid = false;
  public valid = false;
  constructor(public readonly rules: Rule<T>[]) { }
  public validate(validateData: T): boolean {
    this.errorBug = [];
    for (const rule of this.rules) {
      const ruleResult = rule(validateData);
      if (typeof ruleResult === 'string') this.errorBug.push(ruleResult);
    }
    this.validated = !this.errorBug.length;
    this.valid = this.validated;
    this.invalid = !this.valid;
    return this.validated;
  }
  public errorBugHtml(): string {
    return this.errorBug.join('</br>');
  }
  public static RequiredRule<F>(message = "Поле должно быть заполнено"): Rule<F> {
    return (validateData: F) => !!validateData || message;

  }
}