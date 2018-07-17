# Project Title

TMUL - Not So Fast Multiplication

## Algorytm

Zadanie [TMUL](https://pl.spoj.com/problems/TMUL/) na podstawie algorytmu: [video algorytm](https://www.youtube.com/watch?v=b_xUE4wkVKY)

1. Ustalenie max rozmiaru kontenera (w 32 bitowych systemach jest to dla PHP wartość od  -2,147,483,648 to 2,147,483,647) a zatem kontener powinien
zawierać max. cyfry 9999x9999 < 2,147,483,647, rozmiar kontenera oznaczamy jako $container

2. Po pobraniu danych wejściowych pobieramy ilość testów i zapisujemy w zmiennej $ilosc_testow
oraz sprawdzamy czy wartości absolutne obydwu liczb do przemonożenia są mniejsze 
od wartości granicznych 999999999 lub 9999, to zależy od wielkości PHP_INT_SIZE
2a. Jeżeli są mniejsze to zwracamy ich wynik czyli  $x*$y, jeśli $x = 0 lub $y = 0 to zwracamy 0, jeśli $x = 1 to wracamy $y i vice versa 

2b. Jeżeli żaden z powyższych warunków nie jest spełniony to przechodzimy do punktu 3

3. Przechodzimy do pętli po $ilosc_testow, gdzie licznikiem jest $k

4. Rozbijamy podane liczby na tablice o nazwach $x i $y, gdzie kazda tablica sklada sie z elementow o rozmiarze $container zwierajacych cyfry
z liczby poczynając od końca. Kiedy jedna z liczb będzie krótsza uzupełniamy ją jako pojedyncze 0.
```php
$x = str_split((string)$x, $container);
$y = str_split((string)$y, $container);
if (sizeof($x) < sizeof($y)) {
	$x_append = array_fill(0, $container, '0');
	$x = array_merge($x_append, $x);
} else if (sizeof($x) > sizeof($y)) {
	$y_append = array_fill(0, $container, '0');
	$y = array_merge($y_append, $y);
}
```

5. Tworzymy zmienną $pyramid oraz tablice $suma i dwuwymiarową tablicę $pyramid_bottom. 
Robimy pętle po x lub y (są takiej samej dlugości) licznikiem jest $i a warunkiem $i < sizeof($x)

5a. Robimy petle po x lub y licznikiem jest $j a warunkiem $j+$i < sizeof($x), 
w pętli 5a zapisujemy:
```php
if ($i == 0) 
	$pyramid .= str_pad(x[$j] * y[$j+$i], PHP_INT_SIZE, '0', STR_PAD_LEFT);
if ($i > 0) 
	pyramid_bottom[$i][0] .= str_pad($x[$j] * $y[$j+$i], PHP_INT_SIZE, '0', STR_PAD_LEFT);
	pyramid_bottom[$i][1] .= str_pad($y[$j] * $x[$j+$i], PHP_INT_SIZE, '0', STR_PAD_LEFT);
```

5b. Po zakończeniu pętli z 5a robimy:
```php
if ($i > 0) {
    pyramid_bottom[$i][0] = str_pad($pyramid_bottom[$i][0], sizeof($x)*$container, '0', STR_PAD_BOTH);  //uzupelnienie brakujacymi zerami po odydwu stronach
    pyramid_bottom[$i][1] = str_pad($pyramid_bottom[$i][1], sizeof($x)*$container, '0', STR_PAD_BOTH);
}
```

6. Po zakończeniu pętli z 5, mamy juz całą uzupełnioną zerami strukturę piramidy, pozostaje obliczenie sum i wydrukowanie wyniku dla testu na wyjście:
```php
$row = str_split($pyramid);
$suma_temp = array();
$carry = array();
$result = array();

/* 
	Ograniczenie na kodzie wystepuje jesli przy sumowaniu dostaniemy liczbe wieksza niż to co sie moze zmiescic dla kontenera a wiec
	w systemie 32 bitowym liczba wieksza nic 2,147,483,647 wieksza nic 9,223,372,036,854,775,807
	tutaj zalozenie jest takie ze w wypadku liczb o dlugosci do 10000 cyfr raczej nie powinno sie to zdarzyc ale to jest kolejny case
	do przebadania,
	Można tez zoptymalizowac mnozenie liczb roznej wielkosci kiedy jedna jest wyraznie dluzsza od drugiej, w tym momencie program zawsze
	wykona maksymalnie tyle operacji zeby obsluzyc dluzsza liczbe
*/

foreach ($row as $key => $element) {
	
	$row1 = str_split($pyramid_bottom[$key][0]);
	$row2 = str_split($pyramid_bottom[$key][1]);
	
	$suma_temp[$key] += intval($row1[$key]);
	$suma_temp[$key] += intval($row2[$key]);
	$suma_temp[$key] += intval($element);
	
	if ($key > 0) {
		$suma_temp[$key] += $carry[$key - 1] % 10; //dodajemy cyfre z przeniesienia
	} 
	
	$carry[$key] = intval($suma_temp[$key] / 10);
	$result[$key] = $suma_temp[$key] % 10;  //pobranie ostaniej cyfry ze slupka w dodawaniu, result przechowuje pojedyncze cyfry
}
```

7. Po zakończeniu pętli z licznikiem $i. Dla każdego testu wypisujemy na wyjście:  
```php
echo ltrim(implode('', $result), '0');
```



