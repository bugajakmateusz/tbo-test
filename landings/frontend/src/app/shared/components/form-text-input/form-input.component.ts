import { Component, Input } from '@angular/core';
import { FormControl } from '@angular/forms';

@Component({
  selector: 'app-form-input',
  templateUrl: './form-input.component.html',
  styleUrls: ['./form-input.component.scss'],
})
export class FormInputComponent {
  @Input({ required: true }) name!: string;
  @Input() type = 'text';
  @Input() min: number = -1000000;
  @Input() max: number = 1000000;
  @Input({ required: true }) control!: FormControl<string | null>;
}
