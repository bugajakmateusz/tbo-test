import { TestBed } from '@angular/core/testing';

import { MachinesMapperService } from './machines-mapper.service';

describe('MachinesMapperService', () => {
  let service: MachinesMapperService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(MachinesMapperService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
