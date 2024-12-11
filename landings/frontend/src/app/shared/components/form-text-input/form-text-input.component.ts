import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-form-text-input',
  templateUrl: './form-text-input.component.html',
  styleUrls: ['./form-text-input.component.scss'],
})
export class FormTextInputComponent {
  @Input({ required: true }) name!: string;
  @Input() type = 'text';
}
