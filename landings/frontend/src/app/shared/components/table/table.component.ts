import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';

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

  @Output() actionChosen = new EventEmitter<{
    action: string;
    id: string;
  }>();

  ngOnInit(): void {}

  chooseAction(action: string, id: string) {
    this.actionChosen.emit({ action, id });
  }
}
