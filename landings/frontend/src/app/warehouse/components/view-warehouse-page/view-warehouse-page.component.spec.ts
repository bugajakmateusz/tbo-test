import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ViewWarehousePageComponent } from './view-warehouse-page.component';

describe('ViewWarehousePageComponent', () => {
  let component: ViewWarehousePageComponent;
  let fixture: ComponentFixture<ViewWarehousePageComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ViewWarehousePageComponent]
    });
    fixture = TestBed.createComponent(ViewWarehousePageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
