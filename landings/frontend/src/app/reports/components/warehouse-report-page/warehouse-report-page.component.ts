import { Component } from '@angular/core';
import { FormBuilder } from '@angular/forms';
import { ReportsService } from '../../services/reports.service';

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
  constructor(
    private fb: FormBuilder,
    private reportsService: ReportsService
  ) {}

  onSubmit() {
    this.reportsService.createWarehouseReport(
      this.form.value.dateFrom!,
      this.form.value.dateTo!
    );
  }
}
