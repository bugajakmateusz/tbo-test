import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-form-button',
  templateUrl: './form-button.component.html',
  styleUrls: ['./form-button.component.scss'],
})
export class FormButtonComponent {
  @Input({ required: true }) text!: string;
  @Input('btn-style') btnStyle = '';
  @Input() color = 'primary';
  @Input() disabled: boolean = false;

  buttonStyling(): string {
    return this.btnStyle === 'outline'
      ? `btn-outline-${this.color}`
      : `btn-${this.color}`;
  }
}
