import { ComponentFixture, TestBed } from '@angular/core/testing';

import { WarehouseReportPageComponent } from './warehouse-report-page.component';

describe('WarehouseReportPageComponent', () => {
  let component: WarehouseReportPageComponent;
  let fixture: ComponentFixture<WarehouseReportPageComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [WarehouseReportPageComponent]
    });
    fixture = TestBed.createComponent(WarehouseReportPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
