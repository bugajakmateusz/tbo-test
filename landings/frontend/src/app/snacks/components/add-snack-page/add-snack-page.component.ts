import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { SnacksService } from '../../services/snacks.service';
import { MachinesService } from 'src/app/machines/services/machines.service';
import { Machine } from 'src/app/machines/models/machine.model';

@Component({
  selector: 'app-add-snack-page',
  templateUrl: './add-snack-page.component.html',
  styleUrls: ['./add-snack-page.component.scss'],
})
export class AddSnackPageComponent implements OnInit {
  showNameForm = true;

  machines: Machine[] = [];

  form = this.fb.group({
    name: ['', Validators.required],
  });

  pricesForm = this.fb.group({});

  setAllPricesForm = this.fb.group({
    price: [''],
  });

  constructor(
    private fb: FormBuilder,
    private snacksService: SnacksService,
    private machinesService: MachinesService
  ) {}

  ngOnInit(): void {
    this.machines = this.machinesService.getMachines();
    this.machines.forEach((machine, index) => {
      const controlName = `machine_${index}`;
      this.pricesForm.addControl(
        controlName,
        this.fb.control('', Validators.required)
      );
    });
  }

  goNext() {
    this.setAllPricesForm.setValue({ price: '' });
    this.setPriceInAllMachines();
    if (this.form.valid) {
      this.showNameForm = false;
    }
  }

  goBack() {
    this.form.setValue({ name: '' });
    this.showNameForm = true;
  }

  onSubmit() {
    const prices = [];
    for (const controlName in this.pricesForm.controls) {
      prices.push({
        machineName: controlName,
        priceInMachine: this.pricesForm.get(controlName)!.value,
      });
    }
    this.snacksService.addSnack(this.form.value.name!, prices);

    this.goBack();
  }

  setPriceInAllMachines() {
    const price = this.setAllPricesForm.value.price;
    this.pricesForm = new FormGroup({});
    this.machines.forEach((machine, index) => {
      const controlName = `machine_${index}`;
      this.pricesForm.addControl(
        controlName,
        this.fb.control(price, Validators.required)
      );
    });
  }
}
