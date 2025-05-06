import pygame
import random

# Initialize Pygame
pygame.init()

# Set up the game window
width = 800
height = 600
window = pygame.display.set_mode((width, height))
pygame.display.set_caption("Snake Game")

# Colors
BLACK = (0, 0, 0)
WHITE = (255, 255, 255)
RED = (255, 0, 0)
GREEN = (0, 255, 0)

# Snake properties
snake_block = 20
snake_speed = 15

# Initialize clock
clock = pygame.time.Clock()

# Font for score display
font = pygame.font.SysFont(None, 50)

class Snake:
    def __init__(self):
        self.body = [[width/2, height/2]]
        self.direction = "RIGHT"

    def move(self):
        head = list(self.body[0])
        if self.direction == "RIGHT":
            head[0] += snake_block
        elif self.direction == "LEFT":
            head[0] -= snake_block
        elif self.direction == "UP":
            head[1] -= snake_block
        elif self.direction == "DOWN":
            head[1] += snake_block
        self.body.insert(0, head)

    def grow(self):
        self.body.append(self.body[-1])

    def check_collision(self):
        head = self.body[0]
        return (
            head[0] < 0
            or head[0] >= width
            or head[1] < 0
            or head[1] >= height
            or head in self.body[1:]
        )

class Food:
    def __init__(self):
        self.position = self.randomize_position()

    def randomize_position(self):
        x = round(random.randrange(0, width - snake_block) / snake_block) * snake_block
        y = round(random.randrange(0, height - snake_block) / snake_block) * snake_block
        return [x, y]

def draw_snake(snake):
    for segment in snake.body:
        pygame.draw.rect(window, GREEN, [segment[0], segment[1], snake_block, snake_block])

def draw_food(food):
    pygame.draw.rect(window, RED, [food.position[0], food.position[1], snake_block, snake_block])

def display_score(score):
    score_text = font.render(f"Score: {score}", True, WHITE)
    window.blit(score_text, [10, 10])

def game_over():
    message = font.render("Game Over!", True, RED)
    window.blit(message, [width/2 - 100, height/2 - 30])
    pygame.display.update()
    pygame.time.wait(2000)

def main():
    game_over_flag = False
    snake = Snake()
    food = Food()
    score = 0

    while not game_over_flag:
        for event in pygame.event.get():
            if event.type == pygame.QUIT:
                game_over_flag = True
            elif event.type == pygame.KEYDOWN:
                if event.key == pygame.K_LEFT and snake.direction != "RIGHT":
                    snake.direction = "LEFT"
                elif event.key == pygame.K_RIGHT and snake.direction != "LEFT":
                    snake.direction = "RIGHT"
                elif event.key == pygame.K_UP and snake.direction != "DOWN":
                    snake.direction = "UP"
                elif event.key == pygame.K_DOWN and snake.direction != "UP":
                    snake.direction = "DOWN"

        snake.move()

        if snake.body[0] == food.position:
            snake.grow()
            food.position = food.randomize_position()
            score += 1

        if snake.check_collision():
            game_over_flag = True

        window.fill(BLACK)
        draw_snake(snake)
        draw_food(food)
        display_score(score)
        pygame.display.update()

        clock.tick(snake_speed)

    game_over()
    pygame.quit()

if __name__ == "__main__":
    main()