import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ViewSnacksPageComponent } from './view-snacks-page.component';

describe('ViewSnacksPageComponent', () => {
  let component: ViewSnacksPageComponent;
  let fixture: ComponentFixture<ViewSnacksPageComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ViewSnacksPageComponent]
    });
    fixture = TestBed.createComponent(ViewSnacksPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
