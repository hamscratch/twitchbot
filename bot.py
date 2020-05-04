import discord
from discord.ext import commands

client = commands.Bot(command_prefix ='.')

@client.event
async def on_ready():
	print('Bot is ready.')

client.run('NzA2OTk4ODg2MzQ2MTI5NDgw.XrCeIQ.WSlpo1QjdzljlH_RS79S6J129q8')