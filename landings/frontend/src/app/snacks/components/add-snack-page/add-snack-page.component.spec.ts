import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AddSnackPageComponent } from './add-snack-page.component';

describe('AddSnackPageComponent', () => {
  let component: AddSnackPageComponent;
  let fixture: ComponentFixture<AddSnackPageComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [AddSnackPageComponent]
    });
    fixture = TestBed.createComponent(AddSnackPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
