import { Component } from '@angular/core';
import { FormBuilder } from '@angular/forms';

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
  constructor(private fb: FormBuilder) {}
}
