import { Component } from '@angular/core';
import { FormBuilder, Validators } from '@angular/forms';
import { ReportsService } from '../../services/reports.service';

@Component({
  selector: 'app-buy-sell-report-page',
  templateUrl: './buy-sell-report-page.component.html',
  styleUrls: ['./buy-sell-report-page.component.scss'],
})
export class BuySellReportPageComponent {
  form = this.fb.group({
    dateFrom: ['', Validators.required],
    dateTo: ['', Validators.required],
  });
  constructor(
    private fb: FormBuilder,
    private reportsService: ReportsService
  ) {}

  onSubmit() {
    if (!this.submitButtonDisabled()) {
      this.reportsService.createBuySellReport(
        this.form.value.dateFrom!,
        this.form.value.dateTo!
      );
      this.form.reset();
    }
  }

  submitButtonDisabled(): boolean {
    return (
      !this.form.valid || this.form.value.dateFrom! > this.form.value.dateTo!
    );
  }
}
