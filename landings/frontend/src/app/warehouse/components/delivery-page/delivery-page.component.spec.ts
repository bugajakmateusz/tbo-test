import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DeliveryPageComponent } from './delivery-page.component';

describe('DeliveryPageComponent', () => {
  let component: DeliveryPageComponent;
  let fixture: ComponentFixture<DeliveryPageComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [DeliveryPageComponent]
    });
    fixture = TestBed.createComponent(DeliveryPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
