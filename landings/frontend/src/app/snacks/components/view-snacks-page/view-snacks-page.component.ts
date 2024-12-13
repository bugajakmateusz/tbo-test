import { Component, OnInit } from '@angular/core';
import { FormBuilder } from '@angular/forms';
import { TableComponent } from 'src/app/shared/components/table/table.component';
import { SnacksService } from '../../services/snacks.service';
import { Snack } from '../../models/snack/snack.model';

@Component({
  selector: 'app-view-snacks-page',
  templateUrl: './view-snacks-page.component.html',
  styleUrls: ['./view-snacks-page.component.scss'],
})
export class ViewSnacksPageComponent implements OnInit {
  columns = ['ID', 'Nazwa'];

  snacks: Snack[] = [];

  buttons = [
    { text: 'Edytuj', action: 'editSnack' },
    { text: 'Usuń', action: 'deleteSnack' },
  ];

  form = this.fb.group({
    name: [''],
  });

  constructor(private fb: FormBuilder, private snacksService: SnacksService) {}

  ngOnInit() {
    this.snacks = this.snacksService.getSnacks();
  }

  editSnack() {
    this.snacksService.editSnack(this.form.value.name!);
  }
  deleteSnack() {
    this.snacksService.deleteSnack();
  }

  onActionChosen(event: { id: string; action: string }) {
    this.snacksService.action = event.action;
    this.snacksService.id = event.id;
    this.setFormValuesToSelectedItem();
  }

  setFormValuesToSelectedItem() {
    const snack = this.snacksService.getCurrentSnack();
    this.form.setValue({
      name: snack.name,
    });
  }

  onCallbackCalled() {
    switch (this.snacksService.action) {
      case 'editSnack': {
        this.editSnack();
        break;
      }
      case 'deleteSnack': {
        this.deleteSnack();
      }
    }
  }
}
