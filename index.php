<?php 

    class Imul {
        public $container = 4;
        public $x; //pierwsza liczba do przemnożenia
        public $x_arr = []; //kontener tablicowy na pierwszą liczbę 
        public $y; //druga liczba do przemnożenia
        public $y_arr = []; //kontener tablicowy na drugą liczbę
        public $result = false; //wynik działania
        public $pyramid;
        public $sign = '';
        public $suma = array();
        public $pyramid_bottom = array();
        public $output = array();

        //rozmiar kontenera liczony w zależności od tego czy system 32 - bitowy czy 64 - bitowy
        public function getContainerSize() {
            $this->container = PHP_INT_SIZE == 8 ? 9 : 4;
        }

        /* sprawdzanie znaku, żeby działało dla liczb ujemnych */
        public function checkSigns() {
            if (
                (strpos($this->x, '-') !== false && strpos($this->y, '-') === false) ||
                (strpos($this->x, '-') === false && strpos($this->y, '-') !== false)
                ) {
                $this->sign = '-';
            } else {
                $this->sign = '';
            }
            $this->x = preg_replace('/\D/', '', $this->x);
            $this->y = preg_replace('/\D/', '', $this->y);
        }

        public function simpleMultiply() {
            if (strlen($this->x) <= $this->container && strlen($this->y) <= $this->container) {
                $this->result = $this->x * $this->y;
            }
            elseif ($this->x == '0' || $this->y == '0') {
                $this->result = 0;
            }
            elseif ($this->x == '1') {
                $this->result = $this->y;
            }
            elseif ($this->y == '1') {
                $this->result = $this->x;
            }
        }

        /**
         * Funkcja dzieli pobrane liczby $x i $y na równe sobie tablice
         * Kiedy jedna z liczb będzie krótsza uzupełniamy ją jako pojedyncze 0.
         * Przykład: $x = 4 9485 $y = 21 9458 4398 3458, $container = 4
         * Result: 
         * $this->x_arr = array('0004', '9485')
         * $this->y_arr = array('0021', '9458', '4398', '3458')
         */
        public function splitNumbers() {
            $x = $this->x;
            $y = $this->y;
            
            $cond_x = strlen($this->x) % $this->container;
            $cond_y = strlen($this->y) % $this->container;
            
            //uzupełnij zerami do rozmiaru kontenera
            if ($cond_x > 0) {
                $x = str_pad($x, strlen($x) + $this->container - $cond_x, '0', STR_PAD_LEFT);
            }
            if ($cond_y > 0) {
                $y = str_pad($y, strlen($y) + $this->container - $cond_y, '0', STR_PAD_LEFT);
            }
            
            //uzupełnij zerami do rozmiaru większej liczby
            if (strlen($x) > strlen($y)) {
                $y = str_pad($y, strlen($x), '0', STR_PAD_LEFT);
            }
            
            if (strlen($y) > strlen($x)) {
                $x = str_pad($x, strlen($y), '0', STR_PAD_LEFT);
            }
            
            $this->x_arr = str_split($x, $this->container);
            $this->y_arr = str_split($y, $this->container);
        }

        /**
         * Funkcja wykonuje serie mnożeń i dodawań w dwóch zagnieżdżonych pętlach
         * Ograniczenie jakie występuję to przypadek kiedy po dodawaniu w słupku
         * dla pojedynczych cyfr dostaniemy liczbę większą niż pojemność bitowa
         * dla danego systemu operacyjnego tj. 2^32-1 dla 32-bitowego lub 2^64-1
         * dla 64 bitowego systemu operacyjnego
         */
        public function processNumbers() {
            //zerowanie zmiennych i ustawianie początkowych wartości
            $this->pyramid = '';
            $this->pyramid_bottom = array();
            $this->suma = array();
            $carry = array();
            $licznik = count($this->x_arr);
            $pyramid_length = 0;
            $suma_temp = array_fill(0, $licznik*$this->container*2, '0');
            $pyramid_flag = true;
            for ($i = 0; $i < $licznik; $i++) {
                $gora = '';
                $dol = '';
                for ($j = 0; $j+$i < $licznik; $j++) {
                    if ($i == 0) {
                        $this->pyramid .= str_pad($this->x_arr[$j] * $this->y_arr[$j+$i], $this->container*2, '0', STR_PAD_LEFT);
                        $pyramid_length = strlen($this->pyramid);
                    }
                    elseif ($i > 0) {
                        $gora .= str_pad($this->x_arr[$j] * $this->y_arr[$j+$i], $this->container*2, '0', STR_PAD_LEFT);
                        $dol .= str_pad($this->y_arr[$j] * $this->x_arr[$j+$i], $this->container*2, '0', STR_PAD_LEFT);
                    }    
                }
                if ($i > 0) {
                    $this->pyramid_bottom[] = $gora = str_pad($gora, $pyramid_length, '0', STR_PAD_BOTH);
                    $this->pyramid_bottom[] = $dol = str_pad($dol, $pyramid_length, '0', STR_PAD_BOTH);
                    
                    for ($c = $pyramid_length - 1, $d = 0; $c >= 0; $c--, $d++) {
                        if ($pyramid_flag) {
                            $suma_temp[$d] += $this->pyramid[$c];
                        } 
                        $suma_temp[$d] += $gora[$c];
                        $suma_temp[$d] += $dol[$c];

                        //jesli ostatni obieg petli
                        if ($i == ($licznik - 1)) {
                            if ($d > 0) {
                                $suma_temp[$d] += $carry[$d - 1] % 10; //dodajemy cyfre z przeniesienia
                            } 
                            
                            $carry[$d] = intval($suma_temp[$d] / 10);
                            $this->suma[$d] = $suma_temp[$d] % 10;
                        }
                    }
                    
                    $pyramid_flag = false;
                }
            }
            
            array_unshift($this->pyramid_bottom, $this->pyramid);
            print_r($this->pyramid_bottom);
            //print_r($this->x_arr);
            //print_r($this->y_arr);
            echo $this->sign . ltrim(implode('', array_reverse($this->suma)), '0');
        }

        public function init() {
            $this->getContainerSize();

            /** pobranie wartosci od uzytkownika */
            $ilosc_testow = stream_get_line(STDIN, 1024, PHP_EOL);
            for ($k = 0; $k < $ilosc_testow; $k++) {
                $this->result = false;
                $liczby = explode(' ', stream_get_line(STDIN, 20001, PHP_EOL));
                $this->x = $liczby[0];
                $this->y = $liczby[1];
                $this->checkSigns();
                $this->simpleMultiply();
                if ($this->result !== false) {
                    echo $this->sign . $this->result . PHP_EOL;
                } else {
                    $this->splitNumbers();
                    $this->processNumbers();
                }
            }
        }
    
        
    }

    $imul = new Imul();
    $imul->init();

?>