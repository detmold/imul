# Project Title

TMUL - Not So Fast Multiplication

## Algorytm

Zadanie [TMUL](https://pl.spoj.com/problems/TMUL/) na podstawie algorytmu: [video algorytm](https://www.youtube.com/watch?v=b_xUE4wkVKY)

1. Ustalenie max rozmiaru kontenera (w 32 bitowych systemach jest to dla PHP wartość od  -2,147,483,648 to 2,147,483,647) a zatem kontener powinien
zawierać max. cyfry 9999x9999 < 2,147,483,647, rozmiar kontenera oznaczamy jako $container

2. Po pobraniu danych wejściowych pobieramy ilość testów i zapisujemy w zmiennej $ilosc_testow
oraz sprawdzamy czy wartości absolutne obydwu liczb do przemonożenia są mniejsze 
od wartości granicznych 999999999 lub 9999, to zależy od wielkości PHP_INT_SIZE
Jeżeli są mniejsze to zwracamy ich wynik czyli  $x*$y, jeśli $x = 0 lub $y = 0 to zwracamy 0, jeśli $x = 1 to wracamy $y i vice versa 

3. Jeżeli żaden z powyższych warunków nie jest spełniony to przechodzimy do punktu 3

4. Przechodzimy do pętli po $ilosc_testow, gdzie licznikiem jest $k

5. Rozbijamy podane liczby na tablice o nazwach $x i $y, gdzie kazda tablica sklada sie z elementow o rozmiarze $container zwierajacych cyfry
z liczby poczynając od końca. Kiedy jedna z liczb będzie krótsza uzupełniamy ją jako pojedyncze 0. To zadanie wykonuje funkcja: **splitNumbers()**

6. Tworzymy zmienną $pyramid oraz tablice $suma i tablicę $pyramid_bottom. 
Robimy pętle po $this->x_arr lub $this->y_arr (są takiej samej dlugości) licznikiem jest $i a warunkiem $i < sizeof($x), pętle nazywamy symbolicznie **6p**

7. Robimy petle po $this->x_arr lub $this->y_arr licznikiem jest $j a warunkiem $j+$i < sizeof($x), pętle nazywamy sybolicznie **7p**
w pętli **7p** zapisujemy:
- mnożenia wykonane na kontenerach z odpowiadającymi sobie licznikami (ale tylko raz w pierwszym obiegu pętli), wynik jako konkatenowamy ciąg znaków zapisujemy do zmiennej: $this->pyramid i uzupełniamy z lewej strony zerami do rozmiaru: $this->container*2
```php
$this->pyramid .= str_pad($this->x_arr[$j] * $this->y_arr[$j+$i], $this->container*2, '0', STR_PAD_LEFT);
```
- mnożenia wykonane na kontenerach po skosie z licznikami przesunętymi o $j pozycji względem $i pozycji. Uzupełniamy to zerami z lewej strony do rozmiaru: $this->container*2. Wyniki zapisujemy w zmiennych $gora i $dol.
```php
$gora .= str_pad($this->x_arr[$j] * $this->y_arr[$j+$i], $this->container*2, '0', STR_PAD_LEFT);
$dol .= str_pad($this->y_arr[$j] * $this->x_arr[$j+$i], $this->container*2, '0', STR_PAD_LEFT);
$this->pyramid_bottom[] = $gora = str_pad($gora, $pyramid_length, '0', STR_PAD_BOTH);
$this->pyramid_bottom[] = $dol = str_pad($dol, $pyramid_length, '0', STR_PAD_BOTH);
```
8. Po zakończeniu pętli **7p** mamy następującą strukturę przykładową:
> $this->pyramid = 12 34 56 78
gdzie spacjami rozdzielamy kolejne wyniki złożone z pojedynczych operacji składających się na pętle **7p** zakładamy że został zakończony tylko 1 obrót pętli **7p**
Teraz dla każdego licznika większego od zera zpętli **6p** -> czyli $i > 0
dodajemy do tablicy $this->pyramid_bottom[] zmienne $gora - $dol uzupełniając zerami symetrycznie po obydwu stronach
```php
$this->pyramid_bottom[] = $gora = str_pad($gora, $pyramid_length, '0', STR_PAD_BOTH);
$this->pyramid_bottom[] = $dol = str_pad($dol, $pyramid_length, '0', STR_PAD_BOTH);
``` 

9. Po zakończeniu pętli **7p** także robimy sumowanie pojedynczych kolumn. Pętle nazywamy **8p**. W przypadku kiedy
jest to ostatni wiersz z pętli nadrzędnej **6p** dodatkowo robimy
- przenoszenie nadmiarów obsługiwane przez: $carry[$d]
- wyznaczanie ostatecznego wyniku mnożenia, obsługiwane przez: $this->suma[$d]

```php
/* 

	!!! UWAGA - kontrolowane ograniczenie przy sumowaniu

	Ograniczenie na kodzie wystepuje jesli przy sumowaniu dostaniemy liczbe wieksza niż to co sie moze zmiescic dla kontenera a wiec
	w systemie 32 bitowym liczba wieksza nic 2,147,483,647 wieksza nic 9,223,372,036,854,775,807
	tutaj zalozenie jest takie ze w wypadku liczb o dlugosci do 10000 cyfr raczej nie powinno sie to zdarzyc ale to jest kolejny case
	do przebadania,
	Można tez zoptymalizowac mnozenie liczb roznej wielkosci kiedy jedna jest wyraznie dluzsza od drugiej, w tym momencie program zawsze
	wykona maksymalnie tyle operacji zeby obsluzyc dluzsza liczbe
*/

```

10. Po zakończeniu pętli z **6p**, mamy juz całą uzupełnioną zerami strukturę piramidy. Wygląda to przykładowo tak: 

```
	Array
(
    [0] => 000000000000001176264593726352690000975461057789971041
    [1] => 000000000000000009185185200341411259263526900000000000
    [2] => 000000000000000033876532200755982320997104100000000000
    [3] => 000000000000000000000000011851851852000000000000000000
    [4] => 000000000000000000000000096790123458000000000000000000
)
```

Jak widać struktura piramidy jest symetrycznie wyrównana i liczby są odpowiednio wypozycjonowane. Sumowanie kolumn dla lepszej optymalizacji robione było w pętli **8p** Zatem ostateczny wynik mnożenia powinien znajdować się w zmiennej $this->suma[]
Po zakończeniu pętli **6p** drukujemy go na ekran w ten sposób:
```php
echo $this->sign . ltrim(implode('', array_reverse($this->suma)), '0');
```