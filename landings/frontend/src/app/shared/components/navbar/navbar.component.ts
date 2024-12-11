import { Component } from '@angular/core';

@Component({
  selector: 'app-navbar',
  templateUrl: './navbar.component.html',
  styleUrls: ['./navbar.component.scss'],
})
export class NavbarComponent {
  complexLinks = [
    {
      name: 'Magazyn',
      icon: 'archive',
      options: [
        {
          name: 'Przeglądaj magazyn',
          path: 'warehouse/view',
        },
        {
          name: 'Przyjmij dostawę',
          path: 'warehouse/delivery',
        },
        {
          name: 'Przekaz towar kurierowi',
          path: 'warehouse/hand-to-courier',
        },
      ],
    },
    {
      name: 'Przekąski',
      icon: 'box-seam',
      options: [
        {
          name: 'Przeglądaj przekąski',
          path: 'snacks/view',
        },
        {
          name: 'Dodaj przekąskę',
          path: 'snacks/add',
        },
      ],
    },
    {
      name: 'Maszyny',
      icon: 'gear',
      options: [
        {
          name: 'Przeglądaj maszyny',
          path: 'machines/view',
        },
        {
          name: 'Dodaj maszynę',
          path: 'machines/add',
        },
        {
          name: 'Zmień ceny w maszynie',
          path: 'machines/change-prices',
        },
      ],
    },
    {
      name: 'Raporty',
      icon: 'graph-up',
      options: [
        {
          name: 'Utwórz raport maszyny/maszyn',
          path: 'reports/machines',
        },
        {
          name: 'Utwórz raport magazynu',
          path: 'reports/warehouse',
        },
        {
          name: 'Utwórz raport zakupu/sprzedazy',
          path: 'reports/buy-sell',
        },
      ],
    },
    {
      name: 'Uzytkownicy',
      icon: 'people',
      options: [
        {
          name: 'Przeglądaj uzytkowników',
          path: 'users/view',
        },
        {
          name: 'Dodaj uzytkownika',
          path: 'users/add',
        },
      ],
    },
  ];
}
