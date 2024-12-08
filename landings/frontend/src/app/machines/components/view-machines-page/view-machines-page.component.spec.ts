import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ViewMachinesPageComponent } from './view-machines-page.component';

describe('ViewMachinesPageComponent', () => {
  let component: ViewMachinesPageComponent;
  let fixture: ComponentFixture<ViewMachinesPageComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ViewMachinesPageComponent]
    });
    fixture = TestBed.createComponent(ViewMachinesPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
