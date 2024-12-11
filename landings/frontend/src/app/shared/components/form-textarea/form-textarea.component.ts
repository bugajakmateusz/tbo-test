import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-form-textarea',
  templateUrl: './form-textarea.component.html',
  styleUrls: ['./form-textarea.component.scss'],
})
export class FormTextareaComponent {
  @Input({ required: true }) name!: string;
}
