import { Component } from '@angular/core';
import { FormBuilder } from '@angular/forms';

@Component({
  selector: 'app-warehouse-report-page',
  templateUrl: './warehouse-report-page.component.html',
  styleUrls: ['./warehouse-report-page.component.scss'],
})
export class WarehouseReportPageComponent {
  form = this.fb.group({
    dateFrom: [''],
    dateTo: [''],
  });
  constructor(private fb: FormBuilder) {}
}
