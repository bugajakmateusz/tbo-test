import { Component } from '@angular/core';
import { FormBuilder } from '@angular/forms';
import { ReportsService } from '../../services/reports.service';

@Component({
  selector: 'app-buy-sell-report-page',
  templateUrl: './buy-sell-report-page.component.html',
  styleUrls: ['./buy-sell-report-page.component.scss'],
})
export class BuySellReportPageComponent {
  form = this.fb.group({
    dateFrom: [''],
    dateTo: [''],
  });
  constructor(
    private fb: FormBuilder,
    private reportsService: ReportsService
  ) {}

  onSubmit() {
    this.reportsService.createBuySellReport(
      this.form.value.dateFrom!,
      this.form.value.dateTo!
    );
  }
}
