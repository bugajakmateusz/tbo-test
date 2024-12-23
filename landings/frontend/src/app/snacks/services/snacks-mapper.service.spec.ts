import { TestBed } from '@angular/core/testing';

import { SnacksMapperService } from './snacks-mapper.service';

describe('SnacksMapperService', () => {
  let service: SnacksMapperService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(SnacksMapperService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
