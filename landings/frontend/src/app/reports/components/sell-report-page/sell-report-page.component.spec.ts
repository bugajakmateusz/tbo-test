import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SellReportPageComponent } from './sell-report-page.component';

describe('SellReportPageComponent', () => {
  let component: SellReportPageComponent;
  let fixture: ComponentFixture<SellReportPageComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [SellReportPageComponent]
    });
    fixture = TestBed.createComponent(SellReportPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
