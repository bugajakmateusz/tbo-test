import { Component } from '@angular/core';

@Component({
  selector: 'app-add-snack-page',
  templateUrl: './add-snack-page.component.html',
  styleUrls: ['./add-snack-page.component.scss'],
})
export class AddSnackPageComponent {
  currentFormIndex = 0;

  machines = ['machine 1', 'machine 2', 'machine 3'];

  setFormIndex(newIndex: number) {
    this.currentFormIndex = newIndex;
  }
}
