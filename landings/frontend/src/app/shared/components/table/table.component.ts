import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { FormGroup } from '@angular/forms';

@Component({
  selector: 'app-table',
  templateUrl: './table.component.html',
  styleUrls: ['./table.component.scss'],
})
export class TableComponent implements OnInit {
  @Input() title = '';
  @Input({ required: true }) columns!: string[];
  @Input({ required: true }) rows!: any[];
  @Input() buttons: {
    text: string;
    action: string;
  }[] = [];
  @Input() formGroup: FormGroup = new FormGroup({});
  @Input() inputs: {
    type: string;
    name: string;
    title: string;
  }[] = [];
  @Input() checkboxes: {
    name: string;
    title: string;
  }[] = [];

  @Output() actionChosen = new EventEmitter<{
    action: string;
    id: string;
  }>();

  ngOnInit(): void {}

  chooseAction(action: string, id: string) {
    this.actionChosen.emit({ action, id });
  }
}
