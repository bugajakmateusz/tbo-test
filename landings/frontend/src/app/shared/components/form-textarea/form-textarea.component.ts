import { Component, Input } from '@angular/core';
import { FormControl } from '@angular/forms';

@Component({
  selector: 'app-form-textarea',
  templateUrl: './form-textarea.component.html',
  styleUrls: ['./form-textarea.component.scss'],
})
export class FormTextareaComponent {
  @Input({ required: true }) name!: string;
  @Input({ required: true }) control!: FormControl<string | null>;
}
