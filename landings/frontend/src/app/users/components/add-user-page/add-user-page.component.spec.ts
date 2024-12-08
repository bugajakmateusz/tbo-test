import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AddUserPageComponent } from './add-user-page.component';

describe('AddUserPageComponent', () => {
  let component: AddUserPageComponent;
  let fixture: ComponentFixture<AddUserPageComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [AddUserPageComponent]
    });
    fixture = TestBed.createComponent(AddUserPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
