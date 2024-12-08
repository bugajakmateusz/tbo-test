import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ChangePricesPageComponent } from './change-prices-page.component';

describe('ChangePricesPageComponent', () => {
  let component: ChangePricesPageComponent;
  let fixture: ComponentFixture<ChangePricesPageComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ChangePricesPageComponent]
    });
    fixture = TestBed.createComponent(ChangePricesPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
