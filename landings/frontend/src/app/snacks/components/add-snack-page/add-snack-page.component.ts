import { Component } from '@angular/core';
import { FormBuilder } from '@angular/forms';
import { SnacksService } from '../../services/snacks.service';

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

  form = this.fb.group({
    name: [''],
  });

  pricesForm = this.fb.group({
    price1: [''],
    price2: [''],
    price3: [''],
  });

  setAllPricesForm = this.fb.group({
    price: [''],
  });

  constructor(private fb: FormBuilder, private snacksService: SnacksService) {}

  onSubmit() {
    this.snacksService.addSnack(this.form.value.name!);
  }
}
