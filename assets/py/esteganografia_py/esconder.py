# coding: utf-8

import re
import Image
import sys

# Define quantos pixels serão utilizados para informar o tamanho da mensagem oculta
PIXELS_RESERVADOS = 10

def pixels(tam):
    '''Facilita a iteração pelos pixels da imagem'''
    for y in xrange(tam[1]):
        for x in xrange(tam[0]):
            yield (x, y)


def esteganografar(img_orig, img_esteg, str_bin):
    # Abre a imagem e obtém seus atributos
    img = Image.open(img_orig)
    largura, altura = img.size

    # Verifica se o formato da imagem é compatível e se ela possui capacidade
    if img.mode[:3] != 'RGB' or largura * altura * 3  < len(str_bin) + PIXELS_RESERVADOS * 3:
        raise IndexError('O tamanho da mensagem excede a capacidade da imagem ou não há suporte para a mesma')

    # Os primeiros pixels definem o tamanho da informação a ser ocultada
    bits_tam = bin(len(str_bin))[2:].zfill(PIXELS_RESERVADOS * 3)
    str_bin = bits_tam + str_bin

    # Completa a informação tornando-a múltipla de 3 e iterável;
    str_bin = enumerate(str_bin + '0' * (3 - len(str_bin) % 3))

    # Carrega os pixels da imagem para a memória;
    pix = img.load()

    # Percorre cada pixel da imagem
    for x, y in pixels(img.size):
        try:
            # Altera o valor dos bits menos significativos
            rgb = map(lambda cor, bit: cor - (cor % 2) + int(bit), pix[x, y][:3], [str_bin.next()[1] for _ in xrange(3)])
            pix[x, y] = tuple(rgb)
        except StopIteration:
            # Quando não houver mais bits para se esteganografar, str_bin disparará uma
            # exceção do tipo StopIteration, e a nova imagem estará pronta para ser salva;
            img.save(img_esteg, 'PNG', quality=100)
            return

def gera_bin(msg):
    '''Para cada caractere, obtém o valor binário de seu código ASCII'''
    return ''.join(bin(ord(caractere))[2:].zfill(8) for caractere in msg)


def recupera_str(str_bin):
    '''Converte cada grupo de 8 bits no seu respectivo caractere'''
    return ''.join(chr(int(bin, 2)) for bin in re.findall(r'.{8}', str_bin))

if __name__ == '__main__':
    msg_bin = gera_bin(sys.argv[1])  # Transforma em binário
    imagem_original = 'Gabe.png'  # Recolhe nome da imagem
    hue = esteganografar(imagem_original, 'esteganografia.png', msg_bin)

    print(hue)