import { TestBed } from '@angular/core/testing';

import { WarehouseMapperService } from './warehouse-mapper.service';

describe('WarehouseMapperService', () => {
  let service: WarehouseMapperService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(WarehouseMapperService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
