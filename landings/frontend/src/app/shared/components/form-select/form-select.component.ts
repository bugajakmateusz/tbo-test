import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-form-select',
  templateUrl: './form-select.component.html',
  styleUrls: ['./form-select.component.scss'],
})
export class FormSelectComponent {
  @Input({ required: true }) name!: string;
  @Input({ required: true }) options!: { name: string; value: any }[];
}
