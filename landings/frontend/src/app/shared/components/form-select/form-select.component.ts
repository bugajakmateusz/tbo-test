import { Component, Input } from '@angular/core';
import { FormControl } from '@angular/forms';

@Component({
  selector: 'app-form-select',
  templateUrl: './form-select.component.html',
  styleUrls: ['./form-select.component.scss'],
})
export class FormSelectComponent {
  @Input({ required: true }) name!: string;
  @Input({ required: true }) options!: { name: string; value: any }[];
  @Input({ required: true }) control!: FormControl<string | null>;
}
