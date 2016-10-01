# coding: utf-8

import re
import Image

# Define quantos pixels serão utilizados para informar o tamanho da mensagem oculta
PIXELS_RESERVADOS = 10

def pixels(tam):
    '''Facilita a iteração pelos pixels da imagem'''
    for y in xrange(tam[1]):
        for x in xrange(tam[0]):
            yield (x, y)

def recuperar(img_esteg):
    # Abre a imagem, obtém seus atributos e carrega os pixels para a memória
    img = Image.open(img_esteg)
    tam = img.size
    pix = img.load()

    # Obtém os primeiros pixels, que definem o tamanho da informação embutida;
    info_tam = ''
    for p in pixels(tam):
        info_tam += ''.join('1' if cor % 2 else '0' for cor in pix[p][:3])
        if len(info_tam) >= PIXELS_RESERVADOS * 3:
            info_tam = int(info_tam, 2)
            break

    # Extrai a informação binária da imagem
    info_bin = ''
    for p in pixels(tam):
        info_bin += ''.join('1' if cor % 2 else '0' for cor in pix[p][:3])

    return info_bin[PIXELS_RESERVADOS * 3:info_tam + PIXELS_RESERVADOS * 3]


def gera_bin(msg):
    '''Para cada caractere, obtém o valor binário de seu código ASCII'''
    return ''.join(bin(ord(caractere))[2:].zfill(8) for caractere in msg)


def recupera_str(str_bin):
    '''Converte cada grupo de 8 bits no seu respectivo caractere'''
    return ''.join(chr(int(bin, 2)) for bin in re.findall(r'.{8}', str_bin))

if __name__ == '__main__':
    # Recupera a mensagem
    msg_bin = recuperar('esteganografia.png')
    print(recupera_str(msg_bin))