import { ComponentFixture, TestBed } from '@angular/core/testing';

import { HandToCourierPageComponent } from './hand-to-courier-page.component';

describe('HandToCourierPageComponent', () => {
  let component: HandToCourierPageComponent;
  let fixture: ComponentFixture<HandToCourierPageComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [HandToCourierPageComponent]
    });
    fixture = TestBed.createComponent(HandToCourierPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
