import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AddMachinePageComponent } from './add-machine-page.component';

describe('AddMachinePageComponent', () => {
  let component: AddMachinePageComponent;
  let fixture: ComponentFixture<AddMachinePageComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [AddMachinePageComponent]
    });
    fixture = TestBed.createComponent(AddMachinePageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
