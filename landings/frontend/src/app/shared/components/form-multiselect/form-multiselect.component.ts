import { Component, Input } from '@angular/core';
import { FormControl } from '@angular/forms';

@Component({
  selector: 'app-form-multiselect',
  templateUrl: './form-multiselect.component.html',
  styleUrls: ['./form-multiselect.component.scss'],
})
export class FormMultiselectComponent {
  @Input({ required: true }) name!: string;
  @Input({ required: true }) options!: { name: string; value: any }[];
  @Input() control!: FormControl<string[] | null>;
}

