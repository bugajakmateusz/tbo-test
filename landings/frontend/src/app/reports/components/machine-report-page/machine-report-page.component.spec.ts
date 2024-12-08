import { ComponentFixture, TestBed } from '@angular/core/testing';

import { MachineReportPageComponent } from './machine-report-page.component';

describe('MachineReportPageComponent', () => {
  let component: MachineReportPageComponent;
  let fixture: ComponentFixture<MachineReportPageComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [MachineReportPageComponent]
    });
    fixture = TestBed.createComponent(MachineReportPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
