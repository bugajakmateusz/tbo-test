import { ComponentFixture, TestBed } from '@angular/core/testing';

import { BuyReportPageComponent } from './buy-report-page.component';

describe('BuyReportPageComponent', () => {
  let component: BuyReportPageComponent;
  let fixture: ComponentFixture<BuyReportPageComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [BuyReportPageComponent]
    });
    fixture = TestBed.createComponent(BuyReportPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
