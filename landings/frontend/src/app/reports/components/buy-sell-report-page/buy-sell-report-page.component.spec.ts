import { ComponentFixture, TestBed } from '@angular/core/testing';

import { BuySellReportPageComponent } from './buy-sell-report-page.component';

describe('BuySellReportPageComponent', () => {
  let component: BuySellReportPageComponent;
  let fixture: ComponentFixture<BuySellReportPageComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [BuySellReportPageComponent]
    });
    fixture = TestBed.createComponent(BuySellReportPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
